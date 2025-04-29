import { ref } from 'vue';
import api from '@/services/api'; // Adjust the import path

export function useFileExport() {
    const isCSVExporting = ref(false);
    const isPDFExporting = ref(false);

    function exportFile(endpoint, fileType, defaultFilename, loadingRef) {
        loadingRef.value = true;

        return api
            .get(endpoint, {
                responseType: 'blob',
                headers: {
                    Accept: `application/${fileType === 'csv' ? 'text/csv' : 'pdf'}`,
                },
            })
            .then((response) => {
                const contentDisposition = response.headers['content-disposition'];
                let filename = defaultFilename;

                if (contentDisposition) {
                    const filenameMatch = contentDisposition.match(/filename="?([^"]+)"?/);
                    filename = filenameMatch?.[1] || defaultFilename;
                }

                const blob = new Blob([response.data], {
                    type: `application/${fileType === 'csv' ? 'text/csv' : 'pdf'}`,
                });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');

                link.href = url;
                link.download = filename;
                link.style.display = 'none';

                document.body.appendChild(link);
                link.click();

                window.URL.revokeObjectURL(url);
                document.body.removeChild(link);
            })
            .catch((error) => {
                console.error(`Error exporting ${fileType.toUpperCase()}:`, error);
                throw error;
            })
            .finally(() => {
                loadingRef.value = false;
            });
    }

    return {
        isCSVExporting,
        isPDFExporting,
        exportFile,
    };
}